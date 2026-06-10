<?php

namespace frontend\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\SakipKebijakan;

/**
 * SakipKebijakanSearch represents the model behind the search form of `frontend\models\SakipKebijakan`.
 */
class SakipKebijakanSearch extends SakipKebijakan
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['refkebijakan_id', 'refskpd_id', 'refstrategi_id', 'refmisi_id', 'refsasaranrenstra_id', 'refsasaran_id', 'reftujuan_id', 'refperiode_5tahun_id'], 'integer'],
            [['uraian_kebijakan', 'user_create', 'date_create', 'user_edit', 'date_edit', 'user_delete', 'date_delete'], 'safe'],
            [['uraian_kebijakan'], 'string'],
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
        $query = SakipKebijakan::find();

        // Eager load relations for performance
        $query->with(['refPeriode', 'strategiRenstra', 'sasaranRenstra']);

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
            'refkebijakan_id' => $this->refkebijakan_id,
            'refskpd_id' => $this->refskpd_id,
            'refstrategi_id' => $this->refstrategi_id,
            'refmisi_id' => $this->refmisi_id,
            'refsasaranrenstra_id' => $this->refsasaranrenstra_id,
            'refsasaran_id' => $this->refsasaran_id,
            'reftujuan_id' => $this->reftujuan_id,
            'refperiode_5tahun_id' => $this->refperiode_5tahun_id,
            'date_create' => $this->date_create,
            'date_edit' => $this->date_edit,
            'date_delete' => $this->date_delete,
        ]);

        $query->andFilterWhere(['like', 'uraian_kebijakan', $this->uraian_kebijakan])
            ->andFilterWhere(['like', 'user_create', $this->user_create])
            ->andFilterWhere(['like', 'user_edit', $this->user_edit])
            ->andFilterWhere(['like', 'user_delete', $this->user_delete]);

        return $dataProvider;
    }
}
