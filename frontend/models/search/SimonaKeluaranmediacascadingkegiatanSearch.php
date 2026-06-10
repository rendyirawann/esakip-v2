<?php

namespace frontend\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\SimonaKeluaranmediacascadingkegiatan;

/**
 * SimonaKeluaranmediacascadingkegiatanSearch represents the model behind the search form of `frontend\models\SimonaKeluaranmediacascadingkegiatan`.
 */
class SimonaKeluaranmediacascadingkegiatanSearch extends SimonaKeluaranmediacascadingkegiatan
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['refsimonakeluaranmediacascadingkegiatan_id', 'refsimonacascadingkegiatan_id', 'refcascadingkegiatan_id', 'refkegiatan_id', 'refuser_id', 'refskpd_id', 'refsimonarincianbelanjacascadingkegiatan_id'], 'integer'],
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
        $query = SimonaKeluaranmediacascadingkegiatan::find();

        // Eager load relations for performance
        $query->with(['refProgram', 'refKegiatan']);

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
            'refsimonakeluaranmediacascadingkegiatan_id' => $this->refsimonakeluaranmediacascadingkegiatan_id,
            'refsimonacascadingkegiatan_id' => $this->refsimonacascadingkegiatan_id,
            'refcascadingkegiatan_id' => $this->refcascadingkegiatan_id,
            'refkegiatan_id' => $this->refkegiatan_id,
            'refsimonarincianbelanjacascadingkegiatan_id' => $this->refsimonarincianbelanjacascadingkegiatan_id,
            'refuser_id' => $this->refuser_id,
            'refskpd_id' => $this->refskpd_id,
        ]);

        $query->andFilterWhere(['like', 'file', $this->file])
            ->andFilterWhere(['like', 'nama_file', $this->nama_file]);

        return $dataProvider;
    }
}
