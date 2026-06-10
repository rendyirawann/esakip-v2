<?php

namespace backend\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\SakipSasaran;

/**
 * SakipSasaranSearch represents the model behind the search form of `backend\models\SakipSasaran`.
 */
class SakipSasaranSearch extends SakipSasaran
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['refsasaran_id', 'refperiode_5tahun_id', 'refvisi_id', 'refmisi_id', 'reftujuan_id'], 'integer'],
            [['uraian_sasaran', 'sasaran_isaktif'], 'safe'],
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
        $query = SakipSasaran::find();

        // add conditions that should always apply here
        $query->with(['visi', 'misi', 'tujuan', 'periode']);

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
            'refsasaran_id' => $this->refsasaran_id,
            'refperiode_5tahun_id' => $this->refperiode_5tahun_id,
            'refvisi_id' => $this->refvisi_id,
            'refmisi_id' => $this->refmisi_id,
            'reftujuan_id' => $this->reftujuan_id,
        ]);

        $query->andFilterWhere(['like', 'uraian_sasaran', $this->uraian_sasaran])
            ->andFilterWhere(['like', 'sasaran_isaktif', $this->sasaran_isaktif]);

        return $dataProvider;
    }
}
