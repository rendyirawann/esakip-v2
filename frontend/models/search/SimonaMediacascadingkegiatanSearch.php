<?php

namespace frontend\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\SimonaMediacascadingkegiatan;

/**
 * SimonaMediacascadingkegiatanSearch represents the model behind the search form of `frontend\models\SimonaMediacascadingkegiatan`.
 */
class SimonaMediacascadingkegiatanSearch extends SimonaMediacascadingkegiatan
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['refsimonamediacascadingkegiatan_id', 'refsimonacascadingkegiatan_id', 'refuser_id', 'refskpd_id'], 'integer'],
            [['file', 'nama_file'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = SimonaMediacascadingkegiatan::find();

        // Eager load relations for performance
        $query->with(['refPeriode']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'refsimonamediacascadingkegiatan_id' => $this->refsimonamediacascadingkegiatan_id,
            'refsimonacascadingkegiatan_id' => $this->refsimonacascadingkegiatan_id,
            'refuser_id' => $this->refuser_id,
            'refskpd_id' => $this->refskpd_id,
        ]);

        $query->andFilterWhere(['like', 'file', $this->file])
            ->andFilterWhere(['like', 'nama_file', $this->nama_file]);

        return $dataProvider;
    }
}
